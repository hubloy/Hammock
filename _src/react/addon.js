import React from 'react';
import ReactDOM from 'react-dom';

import SiteAddons from './admin/containers/subsite/Addon';

document.addEventListener('DOMContentLoaded', function () {
	const hubloy_membershipContainer = document.getElementById( 'hubloy_membership-addon-container' );
	if ( hubloy_membershipContainer !== null ) {
		ReactDOM.render(<SiteAddons hubloy_membership={window.hubloy_membership} />, hubloy_membershipContainer);
	}
});