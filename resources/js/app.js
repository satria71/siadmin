import './bootstrap';
import { createApp, h } from 'vue'
import { createInertiaApp } from '@inertiajs/vue3'
import 'bootstrap/dist/js/bootstrap.bundle.min.js'
import '@tabler/core/dist/css/tabler.min.css'
import '@tabler/core/dist/js/tabler.min.js'
import '@tabler/core/dist/css/tabler-vendors.min.css'

import { library } from '@fortawesome/fontawesome-svg-core'
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome'
import { faUser, faRightFromBracket } from '@fortawesome/free-solid-svg-icons'
import { faUser as faUserRegular, faHome, faHand, faAddressCard, faBell, faMoon, 
    faArrowAltCircleRight, faFileArchive, faAlarmClock, faFileClipboard, faBuilding,
    faPenToSquare, faSquarePlus} from '@fortawesome/free-regular-svg-icons'
// import { faFacebook, faGithub } from '@fortawesome/free-brands-svg-icons'

library.add(faUser, faHome, faRightFromBracket, faUserRegular, faHand, faAddressCard
    , faBell, faMoon, faArrowAltCircleRight, faFileArchive, faAlarmClock, 
    faFileClipboard, faPenToSquare, faBuilding, faSquarePlus
)

createInertiaApp({
    resolve: name => {
        const pages = import.meta.glob('./Pages/**/*.vue', { eager: true })
        return pages[`./Pages/${name}.vue`]
    },
    setup({ el, App, props, plugin }) {
        createApp({ render: () => h(App, props) })
            .use(plugin)
            .component('font-awesome-icon', FontAwesomeIcon)
            .mount(el)
    },
})
