"use strict";
var lat;
var lng;
var map;
var styles = [{
    "featureType": "administrative",
    "elementType": "labels.text",
    "stylers": [{"visibility": "off"}]
}, {
    "featureType": "administrative",
    "elementType": "labels.text.fill",
    "stylers": [{"color": "#444444"}]
}, {"featureType": "landscape", "elementType": "all", "stylers": [{"color": "#f2f2f2"}]}, {
    "featureType": "poi",
    "elementType": "all",
    "stylers": [{"visibility": "off"}]
}, {
    "featureType": "road",
    "elementType": "all",
    "stylers": [{"saturation": -100}, {"lightness": 45}]
}, {
    "featureType": "road.highway",
    "elementType": "all",
    "stylers": [{"visibility": "simplified"}]
}, {
    "featureType": "road.arterial",
    "elementType": "labels.icon",
    "stylers": [{"visibility": "off"}]
}, {"featureType": "transit", "elementType": "all", "stylers": [{"visibility": "off"}]}, {
    "featureType": "water",
    "elementType": "all",
    "stylers": [{"color": "#46bcec"}, {"visibility": "on"}]
}, {"featureType": "water", "elementType": "geometry.fill", "stylers": [{"color": "#85d6df"}]}, {
    "featureType": "water",
    "elementType": "labels.text",
    "stylers": [{"visibility": "off"}]
}];

//type your address after "address="
jQuery.getJSON('http://maps.googleapis.com/maps/api/geocode/json?address=528 tenth Avenue Boston, BT 58966&sensor=false', function (data) {
    lat = data.results[0].geometry.location.lat;
    lng = data.results[0].geometry.location.lng;
}).complete(function () {
    dxmapLoadMap();
});

function attachSecretMessage(marker, message) {
    var infowindow = new google.maps.InfoWindow(
        {
            content: message
        });
    google.maps.event.addListener(marker, 'click', function () {
        infowindow.open(map, marker);
    });
}

window.dxmapLoadMap = function () {
    var center = new google.maps.LatLng(lat, lng);
    var anotherCenter = new google.maps.LatLng(lat + 0.5, lng);
    var settings = {
        mapTypeId: google.maps.MapTypeId.ROADMAP,
        zoom: 8,
        draggable: false,
        scrollwheel: false,
        center: anotherCenter,
        styles: styles
    };
    map = new google.maps.Map(document.getElementById('map'), settings);

    var image = 'images/address.png';

    var marker = new google.maps.Marker({
        position: center,
        title: 'Map title',
        map: map,
        icon: image
    });
    marker.setTitle('Map title'.toString());
    //type your map title and description here
    attachSecretMessage(marker, '<h3>Map title</h3>Map HTML description');
}