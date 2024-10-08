define(["jquery", "local_kopere_bi/apexcharts"], function($, ApexCharts) {
    return {
        charts : function() {

            function r0(min) {
                if (min == undefined) return Math.floor((Math.random() * 100));

                while (true) {
                    var value = Math.floor((Math.random() * 100));
                    if (value > min) return value;
                }
            }

            window.Apex = {
                chart   : {foreColor : "#ccc",},
                tooltip : {theme : "dark"},
                grid    : {borderColor : "#535A6C"},
                colors  : ["#41c3f1", "#ec5044"],
            };

            chart_line();
            chart_area();
            chart_column();
            chart_pie();

            function chart_line() {
                var options = {
                    series  : [{
                        name : "A",
                        data : [
                            0, r0(), r0(), r0(), r0(), r0(), r0(), r0(), r0(), r0(), r0(), 0
                        ]
                    }, {
                        name : "B",
                        data : [
                            0, r0(), r0(), r0(), r0(), r0(), r0(), r0(), r0(), r0(), r0(), 0
                        ]
                    }],
                    chart   : {
                        type      : "area",
                        height    : 80,
                        width     : 300,
                        sparkline : {enabled : true}
                    },
                    stroke  : {
                        curve   : "smooth",
                        width   : 4,
                        lineCap : "round"
                    },
                    fill    : {
                        type     : "gradient",
                        gradient : {
                            shadeIntensity : 0,
                            inverseColors  : true,
                            opacityTo      : 0,
                            opacityFrom    : 0
                        }
                    },
                    tooltip : {
                        fixed  : {enabled : false},
                        x      : {show : false},
                        marker : {show : false}
                    }
                };
                var element = document.querySelector("#chart-line");
                var chart = new ApexCharts.default(element, options);
                chart.render();
            }

            function chart_area() {
                var options = {
                    series  : [{
                        name : "A",
                        data : [
                            0, r0(), r0(), r0(), r0(), r0(), r0(), r0(), r0(), r0(), r0(), 0
                        ]
                    }, {
                        name : "B",
                        data : [
                            0, r0(), r0(), r0(), r0(), r0(), r0(), r0(), r0(), r0(), r0(), 0
                        ]
                    }],
                    chart   : {
                        type      : "area",
                        height    : 80,
                        width     : 300,
                        sparkline : {enabled : true}
                    },
                    stroke  : {
                        curve   : "smooth",
                        width   : 0,
                        lineCap : "round"
                    },
                    fill    : {
                        type     : "gradient",
                        gradient : {
                            shadeIntensity : 0,
                            inverseColors  : true,
                            opacityTo      : 0.3,
                            opacityFrom    : .8
                        }
                    },
                    tooltip : {
                        fixed  : {enabled : false},
                        x      : {show : false},
                        marker : {show : false}
                    },
                };
                var element = document.querySelector("#chart-area");
                var chart = new ApexCharts.default(element, options);
                chart.render();
            }

            function chart_column() {
                var options = {
                    series  : [{
                        name : "A",
                        data : [
                            0, r0(), r0(), r0(), r0(), r0(), r0(), r0(), r0(), r0(), r0(), 0
                        ]
                    }, {
                        name : "B",
                        data : [
                            0, r0(), r0(), r0(), r0(), r0(), r0(), r0(), r0(), r0(), r0(), 0
                        ]
                    }],
                    chart   : {
                        type      : "bar",
                        height    : 80,
                        width     : 300,
                        sparkline : {enabled : true}
                    },
                    stroke  : {
                        curve   : "smooth",
                        width   : 0,
                        lineCap : "round"
                    },
                    fill    : {
                        type     : "gradient",
                        gradient : {
                            shadeIntensity : 0,
                            inverseColors  : true
                        }
                    },
                    tooltip : {
                        fixed  : {enabled : false},
                        x      : {show : false},
                        marker : {show : false}
                    },
                };
                var element = document.querySelector("#chart-column");
                var chart = new ApexCharts.default(element, options);
                chart.render();
            }

            function chart_pie() {
                var options = {
                    series     : [r0(10), r0(10), r0(10), r0(10)],
                    labels     : ["A", "B", "C", "D"],
                    colors     : ["#F0785A", "#F0C419", "#556080", "#71C285"],
                    chart      : {
                        width     : 100,
                        height    : 100,
                        type      : "pie",
                        sparkline : {enabled : true}
                    },
                    responsive : [{
                        breakpoint : 480,
                    }]
                };
                var element = document.querySelector("#chart-pie");
                var chart = new ApexCharts.default(element, options);
                chart.render();
            }
        }
    };
});