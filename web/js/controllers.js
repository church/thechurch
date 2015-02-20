var church = angular.module('churchApp', []);

church.controller('NearbyController', ['$scope', function ($scope) {

	if ("geolocation" in navigator) {

		navigator.geolocation.getCurrentPosition(
			function(position) {

				console.log(position);

			},
			function(error) {

				console.log(error);

				// Saying "Not Now" doesn't throw any kind of error at all. :(
				// Not sure how to deal with that.

				// @TODO you can't set $scope here. :/
				if (error.code == error.PERMISSION_DENIED) {
					$scope.error = 'You must give location permission to view this page.';
				}
				else if (error.code == error.POSITION_UNAVAILABLE) {
					$scope.error = 'There was an error determining your location, please try again later.';
				}
				else if (error.code == error.TIMEOUT) {
					$scope.error = 'It took too long to determine your location, please try again later.';
				}
				else {
					$scope.error = 'There was an error determining your location.';
				}

			}
		);

	} else {
		$scope.error = 'Your Browser does not support Geolocation, please try a different web browser.';
	}

	$scope.hello = 'hello world!';

}]);
