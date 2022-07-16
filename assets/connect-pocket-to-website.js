const shortcodeDisplay = document.getElementById('shortcode_display');
if(shortcodeDisplay){
    Array.from(document.querySelectorAll('.shortcode_creator input')).forEach(input => {
        input.addEventListener('change', () => {
            let shortcode = '';

            Array.from(document.querySelectorAll('.shortcode_creator input[type="radio"]')).forEach(input => {
                if(input.checked && input.value !== 'on'){
                    shortcode += ` ${input.name}="${input.value}"`;
                }
            });

            Array.from(document.querySelectorAll('.shortcode_creator input[type="text"], .shortcode_creator input[type="number"]')).forEach(input => {
                if(input.value.trim() !== ''){
                    shortcode += ` ${input.name}="${input.value.trim()}"`;
                }
            });

            shortcodeDisplay.value = `[connect-pocket-to-website${shortcode}]`;
        })
    });
}