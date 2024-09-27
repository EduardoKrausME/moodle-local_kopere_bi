json = {
    chart       : {
        type       : "line",
        height     : 350,
        zoom       : {
            enabled : true
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
        show   : true
    },
    dataLabels  : {
        enabled : false
    },
    tooltip     : {
        followCursor : true
    },
    grid        : {
        show    : true,
        padding : {
            bottom : 0
        }
    },
    legend      : {
        position        : "top",
        horizontalAlign : "right",
        offsetY         : -20
    },
    markers     : {
        size        : 4,
        strokeWidth : 0,
        hover       : {
            size : 6
        }
    },
    plotOptions : {},
    fill        : {
        opacity : 1
    },
    xaxis       : {
        tooltip : {
            enabled : false
        }
    }
}
