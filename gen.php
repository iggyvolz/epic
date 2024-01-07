<?php
require_once __DIR__ . "/vendor/autoload.php";
define("SDK_PATH", $argv[1] ?? null);
if(is_null(SDK_PATH) || !is_dir(SDK_PATH) || !is_file(SDK_PATH . DIRECTORY_SEPARATOR . "Include" . DIRECTORY_SEPARATOR . "eos_base.h")) {
    echo "Must set sdk path as first arg\n";
    exit(-1);
}
foreach(scandir(SDK_PATH . DIRECTORY_SEPARATOR . "Include") as $f) {
    if(!str_ends_with($f, ".h")) continue;
//    echo "$f\n";
    $lines = array_map(trim(...), file(SDK_PATH . DIRECTORY_SEPARATOR . "Include" . DIRECTORY_SEPARATOR . $f));
    for($i=0; $i<count($lines); $i++) {
        if(str_starts_with($lines[$i], "EOS_DECLARE_FUNC")) {
            if(preg_match("/^EOS_DECLARE_FUNC\\(([a-zA-Z *_0-9]+)\\) (EOS_[A-Za-z0-9_]+)\\((.*)\\);$/", $lines[$i], $matches)) {
                [$_, $returnType, $name, $paramStr] = $matches;
                if($paramStr === "void") $paramStr = "";
                $paramsStr = array_map(trim(...), array_filter(explode(",", $paramStr), fn($x) => $x !== ""));
                $params = [];
                foreach ($paramsStr as $paramStr) {
                    $expl = explode(" ", $paramStr);
                    $paramName = array_pop($expl);
                    $type = implode(" ", $expl);
                    $params[$paramName] = $type;
                }
                foreach ($params as $paramName => $type) {
                    echo " $name $paramName: $type\n";
                }
//                echo count($params) . PHP_EOL;
//                $words = explode("_", $name);
//                echo count($words) . PHP_EOL;
//                if(count($params) === 4) echo $name . PHP_EOL;
//                if(count($words) > 3) echo $name . PHP_EOL;
//                echo $name . PHP_EOL;
            }
            else {
                echo "ERROR: failed to match " . $lines[$i] . PHP_EOL;
            }
        }
    }
}