import React from 'react';
import ReactDOM from 'react-dom';

import SiteWizard from './admin/containers/subsite/Wizard';

document.addEventListener('DOMContentLoaded', function () {
	const hubloy_membershipWizardContainer = document.getElementById( 'hubloy_membership-wizard-container' );
	if ( hubloy_membershipWizardContainer !== null ) {
		ReactDOM.render(<SiteWizard hubloy_membership={window.hubloy_membership} />, hubloy_membershipWizardContainer);
	}
});