document.addEventListener('DOMContentLoaded', () => {
    // Load the saved language preference
    const savedLang = localStorage.getItem('selectedLanguage') || 'bg'; // Default to 'bg'
    document.getElementById('language-select').value = savedLang;
    translatePage(savedLang);
});

document.getElementById('language-select').addEventListener('change', function () {
    const lang = this.value;
    
    // Save the selected language to localStorage
    localStorage.setItem('selectedLanguage', lang);
    
    // Translate the page content
    translatePage(lang);
});

async function translatePage(lang) {
    const elementsToTranslate = document.querySelectorAll('[data-translate="true"]');
    
    if (lang === 'bg') {
        // Restore the original text if language is Bulgarian
        elementsToTranslate.forEach(element => {
            if (element.hasAttribute('data-original-text')) {
                element.innerHTML = element.getAttribute('data-original-text');
            }
        });
        return;
    }

    // Save the original text in data-original-text attribute
    elementsToTranslate.forEach(element => {
        if (!element.hasAttribute('data-original-text')) {
            element.setAttribute('data-original-text', element.innerHTML);
        }
    });

    const content = Array.from(elementsToTranslate).map(element => element.innerHTML);

    const translatedContent = await translateContent(content, lang);
    
    elementsToTranslate.forEach((element, index) => {
        element.innerHTML = translatedContent[index];
    });
}

async function translateContent(contentArray, lang) {
    try {
        const response = await fetch('includes/translate.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                content: contentArray,
                target_lang: lang
            })
        });
        const data = await response.json();
        return data.translatedText;
    } catch (error) {
        console.error('Error:', error);
        return contentArray; // Return original content in case of error
    }
}
