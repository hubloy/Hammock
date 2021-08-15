import React from 'react';
import ReactDOM from 'react-dom';

import SiteWizard from './admin/containers/subsite/Wizard';

document.addEventListener('DOMContentLoaded', function () {
	const hammockWizardContainer = document.getElementById( 'hammock-wizard-container' );
	if ( hammockWizardContainer !== null ) {
		ReactDOM.render(<SiteWizard hammock={window.hammock} />, hammockWizardContainer);
	}
});