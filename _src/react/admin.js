import React from 'react';
import ReactDOM from 'react-dom';

import NetworkAdmin from './admin/containers/network/Admin';
import SiteAdmin from './admin/containers/subsite/Admin';

document.addEventListener('DOMContentLoaded', function () {
	const hubloy_membershipContainer = document.getElementById( 'hubloy_membership-admin-container' );
	if ( hubloy_membershipContainer !== null ) {
		if ( window.hubloy_membership.is_multisite ) {
			ReactDOM.render(<NetworkAdmin hubloy_membership={window.hubloy_membership} />, hubloy_membershipContainer);
		} else {
			ReactDOM.render(<SiteAdmin hubloy_membership={window.hubloy_membership} />, hubloy_membershipContainer);
		}
	}
});