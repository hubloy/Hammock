import React from 'react';
import ReactDOM from 'react-dom';
import { HashRouter, Route, Switch } from 'react-router-dom';
import SiteMemberships from './admin/containers/subsite/Memberships';
import Edit from './admin/containers/subsite/memberships/Edit';

document.addEventListener('DOMContentLoaded', function () {
	const jengaPressContainer = document.getElementById('hammock-memberships-container');
	if (jengaPressContainer !== null) {
		const MembershipPage = (props) => <SiteMemberships hammock={window.hammock} {...props} />
		const MembershipEditPage = (props) => <Edit hammock={window.hammock} {...props} />
		const routing = (
			<HashRouter>
				<Switch>
					<Route exact path="/" component={MembershipPage} />
					<Route path="/paged/:page" component={MembershipPage} />
					<Route path="/edit/:id" component={MembershipEditPage} />
				</Switch>
			</HashRouter>
		);
		ReactDOM.render(routing, jengaPressContainer);
	}
});