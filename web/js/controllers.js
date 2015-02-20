var church = angular.module('churchApp', ['geolocation']);

church.controller('NearbyController', function ($scope, geolocation, $http) {

	geolocation.getLocation().then(function(data) {

		var route = Routing.generate('place_nearby_location', {
			'latitude': data.coords.latitude,
			'longitude': data.coords.longitude
		})

		$http.get(route).
			success(function(data, status, headers, config) {
				$scope.hello = data.hello;
			}).
			error(function(data, status, headers, config) {
				$scope.error = 'Something went wrong...';
			});

	},
	function(error) {
		$scope.error = error;
	});

});
