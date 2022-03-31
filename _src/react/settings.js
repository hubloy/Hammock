import React from 'react';
import ReactDOM from 'react-dom';
import { HashRouter, Route, Switch } from 'react-router-dom';

import NotFound from './admin/containers/NotFound';
import SiteSettings from './admin/containers/subsite/Settings';

document.addEventListener('DOMContentLoaded', function () {
	const hubloy_membershipContainer = document.getElementById( 'hubloy_membership-settings-container' );
	if ( hubloy_membershipContainer !== null ) {
		const DefaultView = (props) => <NotFound hubloy_membership={window.hubloy_membership} {...props}/>
		const SettingsPage = (props) => <SiteSettings hubloy_membership={window.hubloy_membership} {...props}/>
		const routing = (
			<HashRouter>
				<Switch>
					<Route exact path="/:section?" component={SettingsPage} />
					<Route component={DefaultView} />
				</Switch>
			</HashRouter>
		);
		ReactDOM.render(routing, hubloy_membershipContainer);
	}
});