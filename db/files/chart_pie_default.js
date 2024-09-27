json = {
    chart       : {
        type       : "donut",
        height     : 400,
        width      : "100%",
        zoom       : {
            enabled : false
        },
        dropShadow : {
            enabled : false,
            top     : 3,
            left    : 2,
            blur    : 4,
            opacity : 1
        }
    },
    stroke      : {
        curve  : "smooth",
        width  : 2,
        show   : true,
        colors : ["transparent"]
    },
    dataLabels  : {
        enabled : false
    },
    tooltip     : {
        followCursor : true
    },
    legend      : {
        position : "left",
        offsetY  : 80
    },
    plotOptions : {
        pie    : {
            customScale : 1,
            donut       : {
                size : 0,
            },
            offsetY     : 20,
        },
        stroke : {
            colors : undefined
        }
    }
}