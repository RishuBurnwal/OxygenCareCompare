"""Start and manage the local Compare Oxygen WordPress project.

The verified runtime is the portable stack in .local/: PHP 8.3, WordPress,
and SQLite. No Docker or external services are required.

Usage:
    python main.py              Start the site and open it in a browser.
    python main.py --no-browser Start without opening a browser.
    python main.py status       Show whether the site is responding.
    python main.py stop         Stop the PHP server started by this script.
    python main.py restart      Stop, then start again.
"""

from __future__ import annotations

import argparse
import ctypes
import os
import subprocess
import sys
import time
import urllib.error
import urllib.request
import webbrowser
from pathlib import Path


ROOT = Path(__file__).resolve().parent
LOCAL = ROOT / ".local"
PHP = LOCAL / "php" / "php.exe"
DOCROOT = LOCAL / "wordpress"
ROUTER = LOCAL / "router.php"
PID_FILE = LOCAL / "server.pid"
LOG_FILE = LOCAL / "server.log"
ERROR_LOG = LOCAL / "server.err"
LOCAL_MU_PLUGIN = DOCROOT / "wp-content" / "mu-plugins" / "coc-local-runtime.php"
DEFAULT_PORT = 8080


class LaunchError(RuntimeError):
    """An actionable local setup or startup error."""


def local_port() -> int:
    """Return the port configured for this local WordPress install.

    The WordPress site URL is stored as http://localhost:8080, so the launcher
    intentionally uses the same port. Changing it here without updating the
    site URL would make WordPress redirect the browser back to 8080.
    """
    return DEFAULT_PORT


def ensure_local_runtime() -> None:
    """Configure WordPress for PHP's single-threaded local development server.

    WP-Cron can make a loopback HTTP request to the same server. That request
    waits forever behind the page request on a single-threaded server, which
    can stall the site for tens of seconds. This local-only mu-plugin disables
    automatic cron; scheduled tasks can still be run manually with WP-CLI.
    """
    LOCAL_MU_PLUGIN.parent.mkdir(parents=True, exist_ok=True)
    if LOCAL_MU_PLUGIN.is_file():
        return
    LOCAL_MU_PLUGIN.write_text(
        "<?php\n"
        "/**\n"
        " * Plugin Name: Compare Oxygen Local Runtime\n"
        " * Description: Local-only settings for the single-threaded PHP server.\n"
        " */\n"
        "if ( ! defined( 'DISABLE_WP_CRON' ) ) {\n"
        "    define( 'DISABLE_WP_CRON', true );\n"
        "}\n",
        encoding="utf-8",
    )


def site_url() -> str:
    return f"http://localhost:{local_port()}/"


def admin_url() -> str:
    return f"http://localhost:{local_port()}/wp-admin/"


def require_paths() -> None:
    missing = [path for path in (PHP, DOCROOT, ROUTER) if not path.exists()]
    if missing:
        formatted = ", ".join(str(path) for path in missing)
        raise LaunchError(
            "The portable WordPress stack is incomplete. Missing: "
            f"{formatted}. Restore the .local/ directory or use the Docker setup."
        )
    if not (DOCROOT / "wp-config.php").is_file():
        raise LaunchError(
            f"WordPress is not configured: {DOCROOT / 'wp-config.php'} is missing."
        )


def read_pid() -> int | None:
    try:
        raw = PID_FILE.read_text(encoding="utf-8").strip()
    except FileNotFoundError:
        return None
    except OSError:
        return None
    try:
        pid = int(raw)
    except ValueError:
        return None
    return pid if pid > 0 else None


def pid_alive(pid: int) -> bool:
    if os.name == "nt":
        process_query_limited_information = 0x1000
        handle = ctypes.windll.kernel32.OpenProcess(
            process_query_limited_information, False, pid
        )
        if not handle:
            return False
        try:
            exit_code = ctypes.c_ulong()
            if not ctypes.windll.kernel32.GetExitCodeProcess(
                handle, ctypes.byref(exit_code)
            ):
                return False
            return exit_code.value == 259  # STILL_ACTIVE
        finally:
            ctypes.windll.kernel32.CloseHandle(handle)
    try:
        os.kill(pid, 0)
    except OSError:
        return False
    return True


def clear_pid() -> None:
    try:
        PID_FILE.unlink()
    except FileNotFoundError:
        pass
    except OSError:
        pass


def http_status(timeout: float = 3.0) -> int | None:
    try:
        with urllib.request.urlopen(site_url(), timeout=timeout) as response:
            return response.status
    except urllib.error.HTTPError as exc:
        return exc.code
    except (urllib.error.URLError, TimeoutError, OSError):
        return None


def wait_for_wordpress(timeout: int) -> None:
    deadline = time.monotonic() + timeout
    while time.monotonic() < deadline:
        status = http_status()
        if status is not None and status < 500:
            return
        time.sleep(0.5)
    raise LaunchError(
        f"WordPress did not respond at {site_url()} within {timeout} seconds. "
        f"Check {ERROR_LOG} and {LOG_FILE} for the PHP server output."
    )


def tail(path: Path, lines: int = 20) -> str:
    try:
        content = path.read_text(encoding="utf-8", errors="replace").splitlines()
    except OSError:
        return ""
    return "\n".join(content[-lines:])


def show_logs() -> None:
    for path in (ERROR_LOG, LOG_FILE):
        excerpt = tail(path)
        if excerpt:
            print(f"\n--- {path.name} (last 20 lines) ---\n{excerpt}")


def process_command_line(pid: int) -> str:
    """Return a Windows process command line, or an empty string if unavailable."""
    if os.name != "nt":
        return ""
    command = (
        "Get-CimInstance Win32_Process -Filter "
        f"\"ProcessId = {pid}\" | Select-Object -ExpandProperty CommandLine"
    )
    try:
        result = subprocess.run(
            ["powershell", "-NoProfile", "-Command", command],
            capture_output=True,
            text=True,
            timeout=10,
        )
    except (OSError, subprocess.TimeoutExpired):
        return ""
    return result.stdout.strip() if result.returncode == 0 else ""


def stop_process(pid: int) -> None:
    if os.name == "nt":
        result = subprocess.run(
            ["taskkill", "/PID", str(pid), "/T", "/F"],
            capture_output=True,
            text=True,
        )
        if result.returncode != 0 and pid_alive(pid):
            detail = (result.stderr or result.stdout).strip()
            raise LaunchError(f"Could not stop PHP server process {pid}: {detail}")
    else:
        os.kill(pid, 15)

    deadline = time.monotonic() + 5
    while time.monotonic() < deadline and pid_alive(pid):
        time.sleep(0.2)


def stop_project() -> None:
    pid = read_pid()
    if pid is None:
        clear_pid()
        if http_status() is not None:
            raise LaunchError(
                f"A server is responding at {site_url()}, but it was not started by this "
                "script (no PID file). Close that server manually, then retry."
            )
        print("The local project is not running.")
        return

    if not pid_alive(pid):
        clear_pid()
        print("The recorded PHP server process is no longer running. Removed stale PID file.")
        return

    # Do not kill an unrelated process if the PID was reused after a reboot.
    command_line = process_command_line(pid)
    if command_line and "php" not in command_line.lower():
        clear_pid()
        raise LaunchError(
            f"PID {pid} no longer belongs to the PHP server. Removed the stale PID file; "
            "stop any other process using the port manually."
        )

    stop_process(pid)
    clear_pid()
    if http_status() is not None:
        raise LaunchError(
            f"The PHP process was stopped, but something is still responding at {site_url()}."
        )
    print("Local project stopped.")


def start_project(timeout: int, open_browser: bool) -> None:
    require_paths()
    ensure_local_runtime()
    status = http_status()
    if status is not None and status < 500:
        print(f"Compare Oxygen is already running at {site_url()}")
        print(f"Admin: {admin_url()}")
        if open_browser:
            webbrowser.open(site_url())
        return
    if status is not None:
        raise LaunchError(
            f"Something is listening at {site_url()} but returned HTTP {status}. "
            "Stop that process or inspect the server logs."
        )

    stale_pid = read_pid()
    if stale_pid is not None:
        if pid_alive(stale_pid):
            raise LaunchError(
                f"PHP server process {stale_pid} is alive but WordPress is not responding. "
                "Run `python main.py stop` first."
            )
        clear_pid()

    LOG_FILE.parent.mkdir(parents=True, exist_ok=True)
    command = [
        str(PHP),
        "-S",
        f"localhost:{local_port()}",
        "-t",
        str(DOCROOT),
        str(ROUTER),
    ]

    creationflags = 0
    if os.name == "nt":
        creationflags = getattr(subprocess, "CREATE_NO_WINDOW", 0) | getattr(
            subprocess, "CREATE_NEW_PROCESS_GROUP", 0
        )

    try:
        with LOG_FILE.open("ab") as stdout, ERROR_LOG.open("ab") as stderr:
            process = subprocess.Popen(
                command,
                cwd=ROOT,
                stdin=subprocess.DEVNULL,
                stdout=stdout,
                stderr=stderr,
                creationflags=creationflags,
                start_new_session=os.name != "nt",
            )
    except OSError as exc:
        raise LaunchError(f"Could not start the PHP server: {exc}") from exc

    PID_FILE.write_text(str(process.pid), encoding="utf-8")
    print(f"Starting PHP server on {site_url()} ...")

    try:
        wait_for_wordpress(timeout)
    except LaunchError:
        # Clean up a process that failed its readiness check so a retry starts cleanly.
        if process.poll() is None:
            try:
                stop_process(process.pid)
            except LaunchError:
                pass
        clear_pid()
        show_logs()
        raise

    print(f"Compare Oxygen is ready at {site_url()}")
    print(f"Admin: {admin_url()}")
    print(f"PHP server PID: {process.pid}")
    if open_browser:
        webbrowser.open(site_url())


def show_status() -> None:
    status = http_status()
    pid = read_pid()
    if status is None:
        print("Local project is not responding.")
        if pid is not None and pid_alive(pid):
            print(f"PHP server process {pid} is running, but WordPress is not responding.")
            print(f"Check {ERROR_LOG} and {LOG_FILE}.")
        elif pid is not None:
            print("Removed stale PID file.")
            clear_pid()
        raise LaunchError(f"No site is available at {site_url()}.")

    print(f"Local project is running at {site_url()} (HTTP {status}).")
    print(f"Admin: {admin_url()}")
    if pid is not None:
        print(f"PHP server PID: {pid}")


def interactive_menu(timeout: int) -> int:
    """Show numbered actions when main.py is run without a command."""
    options = {
        "1": ("Start and open in browser", lambda: start_project(timeout, True)),
        "2": ("Start without opening a browser", lambda: start_project(timeout, False)),
        "3": ("Show status", show_status),
        "4": ("Stop the server", stop_project),
        "5": ("Restart the server", lambda: (stop_project(), start_project(timeout, True))),
    }
    while True:
        print("\nCompare Oxygen local project")
        print("=" * 32)
        for key, (label, _) in options.items():
            print(f"  {key}. {label}")
        print("  0. Exit")
        try:
            choice = input("\nSelect an option [0-5]: ").strip()
        except (EOFError, KeyboardInterrupt):
            print()
            return 0

        if choice in ("0", "q", "quit", "exit"):
            print("Bye.")
            return 0
        selected = options.get(choice)
        if selected is None:
            print("Invalid option. Enter a number from 0 to 5.")
            continue

        print()
        try:
            selected[1]()
        except LaunchError as exc:
            print(f"Error: {exc}", file=sys.stderr)
        print()


def parse_args() -> argparse.Namespace:
    parser = argparse.ArgumentParser(
        description=(
            "Start and manage the local Compare Oxygen WordPress project. "
            "Run without arguments for the numbered menu."
        )
    )
    parser.add_argument(
        "command",
        nargs="?",
        choices=("up", "status", "stop", "restart"),
        help="Direct command; omit it to use the numbered menu.",
    )
    parser.add_argument(
        "--timeout",
        type=int,
        default=60,
        help="Seconds to wait for WordPress readiness (default: 60).",
    )
    parser.add_argument(
        "--no-browser",
        action="store_true",
        help="With the direct 'up' command, start without opening a browser.",
    )
    return parser.parse_args()


def main() -> int:
    args = parse_args()
    if args.timeout < 1:
        print("Error: --timeout must be a positive number of seconds.", file=sys.stderr)
        return 2
    if args.command is None:
        return interactive_menu(args.timeout)
    try:
        if args.command == "status":
            show_status()
        elif args.command == "stop":
            stop_project()
        elif args.command == "restart":
            stop_project()
            start_project(args.timeout, not args.no_browser)
        else:
            start_project(args.timeout, not args.no_browser)
    except LaunchError as exc:
        print(f"Error: {exc}", file=sys.stderr)
        return 1
    return 0


if __name__ == "__main__":
    raise SystemExit(main())
