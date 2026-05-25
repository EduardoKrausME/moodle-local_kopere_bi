// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * script.js
 *
 * @package   biblocks_maps
 * @copyright 2026 Eduardo Kraus {@link https://eduardokraus.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

$(function () {
    $.fn.qtip.defaults.style.classes = 'ui-tooltip-bootstrap';
    $.fn.qtip.defaults.style.def = false;

    map = kartograph.map('#mapa-online');
    atualMapa = 'world';
    symbols = {};

    showMaps(atualMapa);
    setData([]);
    loadMapsData();
});

var listaCidades = [];

function loadMapsData() {
    // aqui carrega os dados do Mapa
    $.getJSON(urlMapsData, function (cities) {
        $("#loading").hide(300);

        var groupCities = {};
        var iterate1 = $.each(cities, function (id, citie) {
            //var name = citie.city_name + "-" + citie.country_code;
            var name = citie.latitude + "-" + citie.longitude;
            if (groupCities[name]) {
                groupCities[name].nb_visits++;
            } else {
                groupCities[name] = citie
            }
        });

        $.when(iterate1).done(function () {
            listaCidades = [];
            var iterate2 = $.each(groupCities, function (id, citie) {
                listaCidades.push(citie);
            });

            $.when(iterate2).done(function () {
                map.removeSymbols();
                setData(listaCidades);
            });
        });

        // Daqui 60 segundos carrega de novo
        setTimeout(loadMapsData, 60000);
    }).fail(function () {
        // Caso de erro, daqui 90 segundos carrega de novo
        setTimeout(loadMapsData, 90000);
    });
}

function showMaps(mapName) {

    atualMapa = mapName;

    var svgMapa = urlResource + mapName + '.svg';
    map.loadMap(svgMapa, function () {
        map.addLayer('terra-outros', {
            click: function (d, p, evt) {
                if (d.pais) {
                    showMaps("country/" + d.pais, listaCidades);
                }
            },
        });
        map.addLayer('terra', {
            click: function (d, p, evt) {
                if (d.pais) {
                    showMaps("country/" + d.pais, listaCidades);
                } else {
                    showMaps('world', listaCidades);
                }
            },
            title: function (data) {
                return data.name;
            }
        });

        $('.qtip').remove();
        setData(listaCidades);
    }, {padding: -20});
}

function setData(cities) {
    scale = kartograph.scale.sqrt(cities.concat([{nb_visits: 0}]), 'nb_visits').range([2, 10]);
    map.addSymbols({
        type: kartograph.Bubble,
        data: cities,
        clustering: 'k-means', // k-means || noverlap
        sortBy: 'radius desc',
        clusteringOpts: {
            tolerance: 0.5,
            maxRatio: 0.9
        },
        aggregate: function (cities) {
            var nc = {nb_visits: 0, city_names: []};
            $.each(cities, function (i, c) {
                nc.nb_visits += c.nb_visits;
                nc.country_name = c.country_name;
                nc.country_code = c.country_code;
                nc.city_names = nc.city_names.concat(c.city_names ? c.city_names : [c.city_name]);
            });
            if ((nc.city_names.length - 1) === 0) {
                nc.city_name = nc.city_names[0];
            } else if ((nc.city_names.length - 1) === 1) {
                nc.city_name = $("#maps_1_city").val()
                    .replace('{a1}', nc.city_names[0]);
            } else {
                nc.city_name =
                    $("#maps_many_city").val()
                        .replace('{a1}', nc.city_names[0])
                        .replace('{a2}', (nc.city_names.length - 1));
            }
            return nc;
        },
        location: function (city) {
            return [city.longitude, city.latitude];
        },
        radius: function (city) {
            return scale(city.nb_visits);
        },
        tooltip: function (city) {
            var msg = '<div><strong>' + city.city_name + '</strong></div>';

            if (city.nb_visits > 1) {
                var texts = $("#maps_onlines").val()
                    .replace('{a1}', city.nb_visits);
                msg += '<div>' + texts + '</div>';
            } else {
                var text = $("#maps_online").val()
                    .replace('{a1}', city.nb_visits);
                msg += '<div>' + text + '</div>';
            }
            return msg;
        },
        click: function (d, p, evt) {
            evt.stopPropagation();

            if (d.country_code == 'BR' && mapName == 'world') {
                showMaps('country/BRA', cities);
            } else if (mapName != 'world') {
                showMaps('world', cities);
            }
        }
    });
}