import {
    fabFacebook,
    fabFacebookF,
    fabInstagram,
    fabOdnoklassniki,
    fabOdnoklassnikiSquare,
    fabTelegram,
    fabTelegramPlane,
    fabVk,
    fabWhatsapp,
    fabYoutube,
    farCalendarAlt,
    farEnvelope,
    farImages,
    farNewspaper,
    fasAngleDown,
    fasAngleUp,
    fasChevronLeft,
    fasChevronRight,
    fasDownload,
    fasFileDownload,
    fasInfoCircle,
    fasPhoneAlt,
    fasPlay,
    fasStar
} from '@quasar/extras/fontawesome-v5'

import {matArrowDropDown, matArrowLeft, matArrowRight, matSearch} from '@quasar/extras/material-icons'

export const icons = {
    fasFileDownload,
    fasChevronRight,
    fasChevronLeft,
    fasAngleDown,
    fasAngleUp,
    farNewspaper,
    fasInfoCircle,
    fabFacebookF,
    fabFacebook,
    fabTelegram,
    fabTelegramPlane,
    fabOdnoklassniki,
    fabYoutube,
    fabVk,
    fabInstagram,
    matArrowLeft,
    matArrowRight,
    matSearch,
    matArrowDropDown,
    fasPlay,
    farImages,
    fasStar,
    fabOdnoklassnikiSquare,
    fasDownload,
    farCalendarAlt,
    fabWhatsapp,
    farEnvelope,
    fasPhoneAlt
}

export function boot({Vue, inject}) {
    inject('$icons', icons)
}

export function request({Vue, router}) {

}
