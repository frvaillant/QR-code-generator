class Console {

    constructor(styles) {
        this.styles = styles ?? {
            "success"  : ['background: green', 'color: white', 'display: block', 'text-align: left', 'padding: 4px 10px'],
            "error"    : ['background: red', 'color: white', 'display: block', 'text-align: left', 'padding: 4px 10px'],
            "head"     : ['background: #ffce00', 'color: black', 'display: block', 'text-align: left', 'padding: 4px 10px'],
            "info"     : ['background: #1565c0', 'color: white', 'display: block', 'text-align: left', 'padding: 4px 10px'],
            "headTitle": ['background: #ffce00', 'color: black', 'display: block', 'font-size: 1.5rem', 'font-weight: bold', 'text-align: left', 'padding: 4px 10px']
        }
    }

    log(message, styles = null) {
        const lines   = message.split('\n')
        const prefix  = styles ? '%c' : ''
        let toPrint   = [], useStyles = []

        lines.forEach((value, index) => {

            toPrint.push(prefix + value.trimLeft())

            if(styles && styles[index]) {
                const style = styles[index]

                if (this.styles[style]) {
                    useStyles.push(this.styles[style].join(';'))
                } else {
                    useStyles.push(this.styles['success'].join(';'))
                }
            }
        })

        const joiner = lines.length > 1 ? '\n' : ''
        message = toPrint.join(joiner)

        console.log(message, ...useStyles)
    }

}