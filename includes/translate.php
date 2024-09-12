<?php
include "dbh.inc.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get the POST data
    $request = json_decode(file_get_contents('php://input'), true);
    $contents = $request['content'];
    $targetLang = $request['target_lang'];

    $translatedTexts = [];

    foreach ($contents as $content) {
        // Check if translation is already cached
        $sqlCheck = "SELECT translated_text FROM translations_cache WHERE original_text = :original_text AND target_lang = :target_lang";
        $stmtCheck = $pdo->prepare($sqlCheck);
        $stmtCheck->execute(['original_text' => $content, 'target_lang' => $targetLang]);
        $cacheResult = $stmtCheck->fetch(PDO::FETCH_ASSOC);

        if ($cacheResult) {
            // Use cached translation
            $translatedTexts[] = $cacheResult['translated_text'];
        } else {
            // Prepare the translation request URL
            $url = "https://translate.googleapis.com/translate_a/single?client=gtx&sl=auto&tl=" . $targetLang . "&dt=t&q=" . urlencode($content);

            // Send a GET request to the URL
            $response = file_get_contents($url);

            // Parse the response
            $translatedArray = json_decode($response, true);
            $translatedText = '';

            foreach ($translatedArray[0] as $sentence) {
                $translatedText .= $sentence[0];
            }

            // Cache the result
            $sqlInsert = "INSERT INTO translations_cache (original_text, target_lang, translated_text) VALUES (:original_text, :target_lang, :translated_text)";
            $stmtInsert = $pdo->prepare($sqlInsert);
            $stmtInsert->execute([
                'original_text' => $content,
                'target_lang' => $targetLang,
                'translated_text' => $translatedText
            ]);

            $translatedTexts[] = $translatedText;
        }
    }

    // Return the translated texts as JSON
    echo json_encode(['translatedText' => $translatedTexts]);
}
?>
