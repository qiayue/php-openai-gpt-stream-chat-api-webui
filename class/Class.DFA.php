
<?php

class DFA
{
    private $root;
    private $words_file;
    private $words_count = 0;

    public function __construct($params)
    {
        $this->words_file = $params['words_file'] ?? '';

        $this->root = new DFA_Node();

        $this->load_words_file();
    }

    private function load_words_file(){
        if(!file_exists($this->words_file)){
            return;
        }
        $words = file($this->words_file);
        foreach ($words as $word) {
            $word = trim($word);
            if(empty($word)){
                continue;
            }
            $this->words_count += 1;
            $this->addWord($word);
        }
    }

    public function is_available(){
        return $this->words_count>0;
    }

    public function addWord($word)
    {
        $node = $this->root;
        for ($i = 0; $i < strlen($word); $i++) {
            $char = $word[$i];
            if (!isset($node->children[$char])) {
                $node->children[$char] = new DFA_Node();
            }
            $node = $node->children[$char];
        }
        $node->isEndOfWord = true;
    }

    public function replaceWords($text)
    {
        $result = '';
        $length = strlen($text);
        for ($i = 0; $i < $length;) {
            $node = $this->root;
            $j = $i;
            $lastMatched = -1;
            while ($j < $length && isset($node->children[$text[$j]])) {
                $node = $node->children[$text[$j]];
                if ($node->isEndOfWord) {
                    $lastMatched = $j;
                }
                $j++;
            }

            if ($lastMatched >= 0) {
                $result .= '\*\*\*';
                $i = $lastMatched + 1;
            } else {
                $result .= $text[$i];
                $i++;
            }
        }
        return $result;
    }

    public function containsSensitiveWords($text)
    {
        $length = strlen($text);
        for ($i = 0; $i < $length;) {
            $node = $this->root;
            $j = $i;
            while ($j < $length && isset($node->children[$text[$j]])) {
                $node = $node->children[$text[$j]];
                if ($node->isEndOfWord) {
                    return true;
                }
                $j++;
            }
            $i++;
        }
        return false;
    }
}

class DFA_Node
{
    public $isEndOfWord;
    public $children;

    public function __construct()
    {
        $this->isEndOfWord = false;
        $this->children = [];
    }
}



/*
$inputText = "需要检测的句子";
$isContain = $dfa->containsSensitiveWords($inputText);

echo "Original Text: \n" . $inputText . "\n";
echo "isContain: " . json_encode($isContain) . "\n";


$outputText = $dfa->replaceWords($inputText);

echo "Original Text: \n" . $inputText . "\n";
echo "Replaced Text: \n" . $outputText . "\n";
*/
