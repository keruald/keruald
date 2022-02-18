<?php
declare(strict_types=1);

namespace Keruald\OmniTools\OS;

class CurrentProcess {

    /**
     * Determines if the current process, ie the PHP interpreter,
     * runs as root on UNIX systems or in elevated mode on Windows.
     *
     * Cygwin processes are considered as Windows processes.
     */
    public static function isPrivileged () : bool {
        if (CurrentOS::isWindows()) {
            // `net session` is known to only work as privileged process.
            // To wrap in cmd allows avoiding /dev/null for Cygwin,
            // or $null when invoked from PowerShell. NUL: will always be used.
            exec('cmd /C "net session >NUL 2>&1"', $_, $exitCode);

            return $exitCode === 0;
        }

        if (!function_exists('posix_geteuid')) {
            // POSIX PHP functions aren't always available, e.g. on FreeBSD
            // In such cases, `id` will probably be available.
            return trim((string)shell_exec('id -u')) === '0';
        }

        return posix_geteuid() === 0;
    }

    /**
     * Determines the username of the current process, ie the PHP interpreter.
     */
    public static function getUsername () : string {
        if (!extension_loaded("posix")) {
            // POSIX PHP functions aren't always available.
            // In such cases, `whoami` will probably be available.
            $username = trim((string)shell_exec("whoami"));

            if (CurrentOS::isWindows() && str_contains('\\', $username)) {
                // Perhaps the domain information could be useful if the user
                // can belong to an AD domain, to disambiguate between local
                // accounts and AD accounts.
                //
                // If not attached to a domain, the PC hostname is used.
                //
                // We return only the username part to be coherent with UNIX.
                return explode('\\', $username, 2)[1];
            }

            return $username;
        }

        return posix_getpwuid(posix_geteuid())["name"];
    }

}
