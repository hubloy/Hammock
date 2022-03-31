import React from 'react';
import ReactDOM from 'react-dom';
import { HashRouter, Route, Switch } from 'react-router-dom';

import SiteMarketing from './admin/containers/subsite/Marketing';

document.addEventListener('DOMContentLoaded', function () {
	const hubloy_membershipContainer = document.getElementById( 'hubloy_membership-marketing-container' );
	if ( hubloy_membershipContainer !== null ) {
		const SiteMarketingPage = (props) => <SiteMarketing hubloy_membership={window.hubloy_membership} {...props} />
		const routing = (
			<HashRouter>
				<Switch>
					<Route exact path="/:page?" component={SiteMarketingPage} />
				</Switch>
			</HashRouter>
		);
		ReactDOM.render(routing, hubloy_membershipContainer);
	}
});