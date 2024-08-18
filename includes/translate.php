<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get the POST data
    $request = json_decode(file_get_contents('php://input'), true);
    $contents = $request['content'];
    $targetLang = $request['target_lang'];

    $translatedTexts = [];

    foreach ($contents as $content) {
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

        $translatedTexts[] = $translatedText;
    }

    // Return the translated texts as JSON
    echo json_encode(['translatedText' => $translatedTexts]);
}
?>
