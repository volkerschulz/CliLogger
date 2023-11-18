<?php

namespace volkerschulz;

class CliLogger {

    protected static bool $bootstrapped = false;
    protected static array $options = [];
    protected static array $ansi_codes = [];
    protected static $log_file = false;
    protected static $std_out = false;
    protected static $std_err = false;
    protected static bool $handles_exceptions = false;
    protected static $previous_exception_handler = null;

    public static function log(string $message, string $type = '') : bool {
        self::bootstrap();

        $format = !empty($type) && isset(self::$options['format'][$type]) ? self::$options['format'][$type] : [];
        $std_err = isset(self::$options['message'][$type]['stderr']) ? self::$options['message'][$type]['stderr'] : false;
        $file_prefix = self::$options['use_file_format_for_cli'];
        self::formatPrintLn(self::addPrefix($message, $type, $file_prefix), $format, $std_err);

        if(is_resource(self::$log_file)) {
            fwrite(self::$log_file, self::addPrefix($message, $type, true) . self::$options['eol']);
        }

        return true;
    }

    public static function error(mixed $message) : bool {
        return self::log(self::objToString($message), 'e');
    }

    public static function success(mixed $message) : bool {
        return self::log(self::objToString($message), 's');
    }

    public static function warning(mixed $message) : bool {
        return self::log(self::objToString($message), 'w');
    }

    public static function notice(mixed $message) : bool {
        return self::log(self::objToString($message), 'n');
    }

    public static function debug(mixed $message) : bool {
        return self::log(self::objToString($message), 'd');
    }

    public static function print(mixed $message) : bool {
        return self::log(self::objToString($message), '');
    }

    public static function cls() : bool {
        self::bootstrap();
        if(!is_resource(self::$std_out))
            return false;

        if(!stream_isatty(self::$std_out))
            return false;

        fwrite(self::$std_out, "\e[2J\e[H");
        return true;
    }

    public static function setOptions(array $options) : bool {
        self::bootstrap();

        self::$options = array_merge(self::$options, $options);

        return true;
    }

    public static function setLogFile(string $filename, string $mode='a') : bool {
        self::bootstrap();
        ob_start();
        $fp = fopen($filename, $mode);
        $output = ob_get_clean();
        if($fp===false) {
            self::log("Error: Failed to open log file!", "e");
            if(!empty($output)) {
                self::log($output, "e");
            }
            return false;
        }
        self::$log_file = $fp;
        return is_resource(self::$log_file);
    }

    public static function handleExceptions(bool $catch = true) : bool {
        self::bootstrap();
        if($catch && !self::$handles_exceptions) {
            self::$previous_exception_handler = set_exception_handler([self::class, 'exception_handler']);
            self::$handles_exceptions = true;
            return true;
        }
        if(!$catch && self::$handles_exceptions) {
            set_exception_handler(self::$previous_exception_handler);
            self::$handles_exceptions = false;
            return true;
        }
        return false;
    }

    public static function exception_handler(\Throwable $exception) {
        self::error('Uncaught exception: ' . $exception->getMessage() . ' in ' . $exception->getFile() . ':' . $exception->getLine());
        self::debug("Stack trace:\n" . $exception->getTraceAsString());
        self::debug("  thrown in " . $exception->getFile() . ':' . $exception->getLine());
        exit($exception->getCode());
    }

    public static function handleErrors(?int $error_levels = null) : bool {
        self::bootstrap();
        if($error_levels === null)
            $error_levels = error_reporting();

        set_error_handler([self::class, 'error_handler'], $error_levels);
        return true;
    }

    public static function error_handler(int $errno, string $errstr, ?string $errfile, ?int $errline) {
        $message = $errstr . ' in ' . $errfile . ':' . $errline;
        switch($errno) {
            case E_ERROR:
            case E_USER_ERROR:
            case E_CORE_ERROR:
            case E_COMPILE_ERROR:
            case E_RECOVERABLE_ERROR:
                self::error($message);
                exit($errno);
            case E_WARNING:
            case E_CORE_WARNING:
            case E_COMPILE_WARNING:
            case E_USER_WARNING:
                self::warning($message);
                return true;
            case E_NOTICE:
            case E_USER_NOTICE:
                self::notice($message);
                return true;
            case E_STRICT:
            case E_DEPRECATED:
            case E_USER_DEPRECATED:
                self::debug($message);
                return true;
        }
        return false;
    }

    private static function objToString(mixed $obj) : string {
        if(!is_string($obj)) {
            ob_start();
            var_dump($obj);
            $obj = ob_get_clean();
        }
        return $obj;
    }
 
    private static function addPrefix(string $message, string $type, bool $file = false) : string {
        $pfx = '';
        if($file) {
            $pfx = isset(self::$options['message'][$type]['file_prefix']) 
                && isset(self::$options['message'][$type]['use_file_prefix']) 
                && self::$options['message'][$type]['use_file_prefix'] 
                ? self::$options['message'][$type]['file_prefix'] : '';
        } else {
            $pfx = isset(self::$options['message'][$type]['cli_prefix']) 
                && isset(self::$options['message'][$type]['use_cli_pfx']) 
                && self::$options['message'][$type]['use_cli_pfx'] 
                ? self::$options['message'][$type]['cli_prefix'] : '';
        }
        // Replaces
        $pfx = str_replace('%datetime%', date(self::$options['datetime_format']), $pfx);

        return $pfx . $message;
    }

    private static function bootstrap() : void {
        if(self::$bootstrapped)
            return;

        register_shutdown_function([self::class, 'shutdown']);

        self::$options = [
            'eol'               => PHP_EOL,
            'datetime_format'   => 'Y-m-d H:i:s',
            'use_file_format_for_cli'   => false,
            'cli_muted'         => false,
            'format'            => [
                'e' => ['red', 'bold'],
                's' => ['green', 'bold'],
                'w' => ['yellow', 'bold'],
                'n' => ['italic'],
                'd' => []
            ],
            'message'           => [
                'e' => [
                    'cli_prefix'        => 'Error: ',
                    'use_cli_pfx'       => true,
                    'file_prefix'       => '%datetime% [Error] ',
                    'use_file_prefix'   => true,
                    'stderr'            => true
                ],
                's' => [
                    'cli_prefix'        => 'Success: ',
                    'use_cli_pfx'       => true,
                    'file_prefix'       => '%datetime% [Success] ',
                    'use_file_prefix'   => true,
                    'stderr'            => true
                ],
                'w' => [
                    'cli_prefix'        => 'Warning: ',
                    'use_cli_pfx'       => true,
                    'file_prefix'       => '%datetime% [Warning] ',
                    'use_file_prefix'   => true,
                    'stderr'            => true
                ],
                'n' => [
                    'cli_prefix'        => 'Notice: ',
                    'use_cli_pfx'       => true,
                    'file_prefix'       => '%datetime% [Notice] ',
                    'use_file_prefix'   => true,
                    'stderr'            => true
                ],
                'd' => [
                    'cli_prefix'        => '',
                    'use_cli_pfx'       => true,
                    'file_prefix'       => '%datetime% ',
                    'use_file_prefix'   => true,
                    'stderr'            => true
                ]
            ]
        ];

        self::$ansi_codes = [
            'bold' => 1, 'dim' => 2, 'italic' => 3, 'underline' => 4, 'blink' => 5, 'strikethrough' => 9,
            'black' => 30, 'red' => 31, 'green' => 32, 'yellow' => 33,'blue' => 34, 'magenta' => 35, 'cyan' => 36, 'white' => 37,
            'blackbg' => 40, 'redbg' => 41, 'greenbg' => 42, 'yellowbg' => 43,'bluebg' => 44, 'magentabg' => 45, 'cyanbg' => 46, 'whitebg' => 47
        ];

        if(defined('STDERR')) {
            self::$std_err = STDERR;
        }

        if(defined('STDOUT')) {
            self::$std_out = STDOUT;
        }

        self::$bootstrapped = true;
    }

    private static function formatPrint(string $text = '', array $format=[], bool $std_err = false, bool $nl = false) : void {
        if(self::$options['cli_muted'])
            return;

        if(empty($text) && !$nl)
            return;

        $pipe = $std_err ? self::$std_err : self::$std_out;

        if((empty($text) && $nl) || (is_resource($pipe) && !stream_isatty($pipe)))
            $format = [];

        if(empty($format)) {
            $output = $text;
        } else {
            $codes = self::$ansi_codes;
            $formatMap = array_map(function ($v) use ($codes) { return $codes[$v]; }, $format);
            $output = "\e[" . implode(';',$formatMap) . 'm'.$text . "\e[0m";
        }

        if(is_resource($pipe)) {
            fwrite($pipe, $output);
            if($nl) fwrite($pipe, self::$options['eol']);
        } else {
            echo $output;
            if($nl) echo self::$options['eol'];
        }
    }
    
    private static function formatPrintLn(string $text='', array $format=[], bool $std_err = false) : void {
        self::formatPrint($text, $format, $std_err, true); 
    }

    private static function shutdown() : void {
        if(is_resource(self::$log_file)) {
            fclose(self::$log_file);
        }
    }
}
