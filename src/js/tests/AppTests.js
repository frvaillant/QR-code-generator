class AppTests {

    constructor() {
        this.messages = {}
        this.console = new Console()
        this.success = 0
        this.tests = 0
        this.errors = 0

    }

    showResume() {
        const add = this.errors === 0 ? ' : App seems to be functional :-)' : ' : errors during tests :-('
        this.console.log(
            'TESTS RESUME' + add + '\n' +
            this.tests + ' tests have ben run.' + '\n' +
            this.success + ' tests were successfull.' + '\n' +
            this.errors + ' tests failed.' + '\n' +
            '-----------------------------------------------', ['headTitle', 'head', 'head', 'head', null]
        )
    }

    showEnd() {
        this.console.log('-----------------------------------------------')
    }

    showMessage(type, msg, num) {
        this.console.log('Test n°' + num + ' : ' + msg, [type])
    }

    setMessage(type, message) {
        this.messages[Object.keys(this.messages).length] = {"type": type, "message": message}
    }

    setInfo(message) {
        this.messages[Object.keys(this.messages).length] = {"type": 'info', "message": message}
    }

    setSuccessMessage(message) {
        this.setMessage('success', message)
        this.success++
        this.tests++
    }

    setErrorMessage(message) {
        this.setMessage('error', message)
        this.errors++
        this.tests++
    }

    testIfColorPickerArePresent() {
        const pickers = document.querySelectorAll('.color:not(.light)')
        if(pickers) {
            this.setSuccessMessage('Color pickers present')
        } else {
            this.setErrorMessage('Color pickers missing')
        }
    }

    testIfLightColorPickerArePresent() {
        const pickers = document.querySelectorAll('.color.light')
        if(pickers) {
            this.setSuccessMessage('Light color pickers present')
        } else {
            this.setErrorMessage('Light color pickers missing')
        }
    }

    testIfColorPickerWorks() {
            const pickers = document.querySelectorAll('.color:not(.light)')
            const picker = pickers[Math.floor(Math.random() * pickers.length)]
            if(!picker)  {
                this.setErrorMessage('Color picker missing - Functional test impossible')
                return
            }
            picker.click()
            if(document.querySelector('input[name="color"]').value === picker.dataset.color) {
                this.setSuccessMessage('Color picker succeeded')
            } else {
                this.setErrorMessage('Color pickers failed')
            }

    }

    testIfLightColorPickerWorks() {
        const pickers = document.querySelectorAll('.color.light')
        const picker = pickers[Math.floor(Math.random() * pickers.length)]
        if(!picker)  {
            this.setErrorMessage('Light color (red) picker missing - Functional test impossible')
            return
        }
        picker.click()
        if(document.querySelector('input[name="light_color"]').value === picker.dataset.color) {
            this.setSuccessMessage('Light color picker succeeded')
        } else {
            this.setErrorMessage('Light color pickers failed')
        }
    }

    testIfQrCodeJsWorks() {
        const picture = document.querySelector('#qrcode img')
        if(picture) {
            this.setSuccessMessage('QrCodeJs ready to work')
        } else {
            this.setErrorMessage('It might be a problem QrCodeJs')
        }
    }

    testIfQrCodeHasBeenGenerated() {
        const picture = document.querySelector('#qrcode img')

        if(picture.getAttribute('src')) {
            this.setSuccessMessage('QrCode generated')
        } else {
            this.setErrorMessage('QrCode has not been generated')
        }

    }

   runTests() {
       this.testIfColorPickerArePresent()
       this.testIfLightColorPickerArePresent()
       this.testIfColorPickerWorks()
       this.testIfLightColorPickerWorks()
       if (document.querySelector('#qrcode')) {
           this.testIfQrCodeJsWorks()
           this.testIfQrCodeHasBeenGenerated()
       } else {
           this.setInfo('Test did not run. Submit form to test QrCode generation')
       }
   }
    async show() {
        setTimeout(() => {
            this.runTests()
            this.showResume()
            let num = 0
            for (let i in this.messages) {
                num++
                const result = this.messages[i]
                this.showMessage(result.type, result.message, num)
            }
            this.showEnd()
        }, 500)

    }
}