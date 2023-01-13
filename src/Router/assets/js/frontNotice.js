
document.addEventListener('DOMContentLoaded', () => {

    const notice = document.createElement('div')
    notice.innerHTML = atob(my_routes_errors.error_message)
    notice.classList.add('router-notice-alert')
    document.querySelector('body').appendChild(notice)

})
