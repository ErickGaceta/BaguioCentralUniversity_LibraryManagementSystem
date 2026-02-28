import Clipboard from '@ryangjchandler/alpine-clipboard'
import MontserratWoff from '../fonts/Montserrat-VariableFont_wght.ttf';
import MontserratItalicWoff from '../fonts/Montserrat-Italic-VariableFont_wght.ttf';

document.addEventListener('alpine:init', () => {
    window.Alpine.plugin(Clipboard)
})
