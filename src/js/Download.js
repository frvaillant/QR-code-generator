const downloadQrCode = (e) => {
    const data = document.querySelector('#qrcode').querySelector('img').getAttribute('src')
    const urlAttributes = e.dataset.url + '_' + e.dataset.size + '_' + e.dataset.quality + '-quality'
    const link = createFakeLink(data, urlAttributes)
    link.click()
    return false
}

const createFakeLink = (data, url) => {
    const link = document.createElement('a')
    link.setAttribute('download', url + '_qrcode.png')
    link.setAttribute('href', data)
    return link
}

const addLoader = (link, delay = 1000) => {
    const originHtml = link.innerHTML
    link.innerHTML += ' <i class="fa-solid fa-spinner fa-spin"></i>'
    setTimeout(() => {
        link.innerHTML = originHtml
    }, delay)
}