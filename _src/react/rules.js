import React from 'react';
import ReactDOM from 'react-dom';
import { HashRouter, Route, Switch } from 'react-router-dom';

import SiteRules from './admin/containers/subsite/Rules';

document.addEventListener('DOMContentLoaded', function () {
	const hubloy_membershipContainer = document.getElementById( 'hubloy_membership-rules-container' );
	if ( hubloy_membershipContainer !== null ) {
		const RulesPage = (props) => <SiteRules hubloy_membership={window.hubloy_membership} {...props} />
		const routing = (
			<HashRouter>
				<Switch>
					<Route exact path="/:section?" component={RulesPage} />
					<Route exact path="/:section?/page/:page?" component={RulesPage} />
				</Switch>
			</HashRouter>
		);
		ReactDOM.render(routing, hubloy_membershipContainer);
	}
});