import { Livewire, Alpine } from '../../vendor/livewire/livewire/dist/livewire.esm';
import Clipboard from '@ryangjchandler/alpine-clipboard'
import MontserratWoff from '../fonts/Montserrat-VariableFont_wght.ttf';
import MontserratItalicWoff from '../fonts/Montserrat-Italic-VariableFont_wght.ttf';

Alpine.plugin(Clipboard)

Livewire.start()
