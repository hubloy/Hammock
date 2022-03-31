import React from 'react';
import ReactDOM from 'react-dom';
import { HashRouter, Route, Switch } from 'react-router-dom';
import SiteMemberships from './admin/containers/subsite/Memberships';
import Edit from './admin/containers/subsite/memberships/Edit';

document.addEventListener('DOMContentLoaded', function () {
	const hubloy_membershipContainer = document.getElementById('hubloy_membership-memberships-container');
	if (hubloy_membershipContainer !== null) {
		const MembershipPage = (props) => <SiteMemberships hubloy_membership={window.hubloy_membership} {...props} />
		const MembershipEditPage = (props) => <Edit hubloy_membership={window.hubloy_membership} {...props} />
		const routing = (
			<HashRouter>
				<Switch>
					<Route exact path="/:page?" component={MembershipPage} />
					<Route path="/edit/:id/:section?" component={MembershipEditPage} />
				</Switch>
			</HashRouter>
		);
		ReactDOM.render(routing, hubloy_membershipContainer);
	}
});