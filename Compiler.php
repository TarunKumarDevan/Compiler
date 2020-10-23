<?php

class Compiler
{
    /** @var array  */
    protected $compiled_values;

    /**
     * Gets the config file and compiles it.
     *
     * @param string $fileName
     */
    public function compile(string $fileName)
    {
        $configFile = fopen($fileName, "r");
        if ($configFile) {
            $this->processConfigFile($configFile);
            fclose($configFile);
        } else {
            echo "error";
        }

        while(true) {
            $key = readline('Enter a key to retrieve the value from config or type exit to exit: ');
            if($key === "exit") {
                break;
            }

            if(array_key_exists($key, $this->compiled_values)) {
                $value = $this->compiled_values[$key];
                $value =  is_bool($value) ? ( $value ? "true" : "false" ) : $value;
                echo $value."\n";
            } else {
                echo  "'$key' key does not exists in config\n";
            }

        }
    }

    /**
     * Process the config file.
     *
     * @param mixed $configFile
     */
    public function processConfigFile($configFile): void
    {
        while (($line = fgets($configFile)) !== false) {
            $line = trim($line);
            if (strpos($line, "#") === 0 || strlen($line) === 0) {

                continue;
            }

            [$key, $value] = explode('=', $line);
            $key = trim($key);
            $value = trim($value);
            if(strlen($key) === 0 || strlen($value) === 0) {
                echo "Error: There is an error in your config file at line '$line' \n";
                exit;
            }
            $value = $this->convertValue($value);
            $this->compiled_values[$key] = $value;
        }
        echo "All keys and values:\n".json_encode($this->compiled_values)."\n\n";
    }

    /**
     * Converts the value to its proper data type.
     *
     * @param string $value
     * @return bool|float|int|string
     */
    public function convertValue(string $value)
    {
        if(preg_match('/^\d+$/',$value)) {
            return (int) $value;
        }

        if(preg_match('/^\d+[.]\d+$/',$value)) {
            return (double) $value;
        }

        switch ($value) {
            case "on" :
            case "yes":
            case "true":
                return true;
            case "off":
            case "no":
            case "false":
                return false;
            default:
                return $value;
        }
    }
}

$compiler = new Compiler();

//update the file name below if you want to run compiler for different file.
$compiler->compile("config.txt");
