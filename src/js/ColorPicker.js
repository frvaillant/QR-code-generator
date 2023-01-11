const deactivatePickers = (targetClass, exceptElement) => {
    const selectedElements = document.querySelectorAll(targetClass)
    if(selectedElements) {
        selectedElements.forEach(selectedElement => {
            if(selectedElement !== exceptElement) {
                selectedElement.classList.remove('active')
            }
        })
    }
}

const makePickersClickable = targetClass => {
    const colorPickers = document.querySelectorAll(targetClass)
    if(colorPickers) {
        colorPickers.forEach(colorPicker => {
            const colorField = document.querySelector('#' + colorPicker.dataset.target)
            colorPicker.addEventListener('click', () => {
                colorPicker.classList.add('active')
                colorField.value = colorPicker.dataset.color
                deactivatePickers(targetClass + '.active', colorPicker)
            })
        })
    }
}

document.addEventListener('DOMContentLoaded', () => {
    makePickersClickable('.color:not(.light)')
    makePickersClickable('.color.light')
})