import React from 'react';
import ReactDOM from 'react-dom';
import { HashRouter, Route, Switch } from 'react-router-dom';

import NotFound from './admin/containers/NotFound';
import SiteMembers from './admin/containers/subsite/Members';
import MemberEdit from './admin/containers/subsite/members/Edit';


document.addEventListener('DOMContentLoaded', function () {
	const hubloy_membershipContainer = document.getElementById( 'hubloy_membership-members-container' );
	if ( hubloy_membershipContainer !== null ) {
		const DefaultView = (props) => <NotFound hubloy_membership={window.hubloy_membership} {...props}/>
		const MembersPage = (props) => <SiteMembers hubloy_membership={window.hubloy_membership} {...props} />
		const MemberViewPage = (props) => <MemberEdit hubloy_membership={window.hubloy_membership} {...props} />
		const routing = (
			<HashRouter>
				<Switch>
					<Route exact path="/:page?" component={MembersPage} />
					<Route exact path="/member/:id/:section?/:page?" component={MemberViewPage} />
					<Route component={DefaultView} />
				</Switch>
			</HashRouter>
		);
		ReactDOM.render(routing, hubloy_membershipContainer);
	}
});