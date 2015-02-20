var church = angular.module('churchApp', ['geolocation']);

church.controller('NearbyController', function ($scope, geolocation, $http) {

	geolocation.getLocation().then(function(data) {

		// @TODO: We need to get the route rather than use an absolute path,
		// otherwise this will never work. :/
		$http.get('/nearby/'+data.coords.latitude+'/'+data.coords.longitude).
		success(function(data, status, headers, config) {
			$scope.hello = data.hello;
		}).
		error(function(data, status, headers, config) {
			$scope.error = 'Something went wrong...';
		});

		console.log(data);
	},
	function(error) {
		$scope.error = error;
	});

});
