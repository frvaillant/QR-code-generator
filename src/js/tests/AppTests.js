class AppTests {

    constructor() {
        this.messages = {}
        this.types = {
            "success": ['background: green', 'color: white', 'display: block', 'text-align: left'],
            "error": ['background: red', 'color: white', 'display: block', 'text-align: left']
        }
    }

    showMessage(type, msg) {
        console.info(msg, this.types[type].join(';'))
    }

    setMessage(type, message) {
        this.messages.push([type, message])
    }

    haveColorPicker() {
        const pickers = document.querySelectorAll('.color:not(.light)')
        if(pickers) {
            this.setMessage('success', 'Color pickers présents')
        } else {
            this.setMessage('error', 'Color pickers absent dans la page')
        }
    }

    run() {
        this.haveColorPicker()
    }

    show() {
        for (let [type, message] in this.messages) {
            this.showMessage(type, message)
        }
    }
}