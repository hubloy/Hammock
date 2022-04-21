import React from 'react';
import ReactDOM from 'react-dom';
import { HashRouter, Route, Switch } from 'react-router-dom';

import NotFound from './admin/containers/NotFound';
import InviteAddonSetting from './admin/containers/subsite/codes/InviteAddonSetting'
import InviteAddonCreate from './admin/containers/subsite/codes/forms/CreateCode'
import InviteAddonEdit from './admin/containers/subsite/codes/forms/EditCode'

document.addEventListener('DOMContentLoaded', function () {
	const hubloy_membershipContainer = document.getElementById( 'hubloy_membership-invites-container' );
	if ( hubloy_membershipContainer !== null ) {
		const DefaultView = (props) => <NotFound hubloy_membership={window.hubloy_membership} {...props}/>
		const InvitesPage = (props) => <InviteAddonSetting hubloy_membership={window.hubloy_membership} {...props} />
		const InvitesCreatePage = (props) => <InviteAddonCreate type={`invitation`} hubloy_membership={window.hubloy_membership} {...props} />
		const InvitesEditPage = (props) => <InviteAddonEdit type={`invitation`} hubloy_membership={window.hubloy_membership} {...props} />
		const routing = (
			<HashRouter>
				<Switch>
					<Route exact path="/" component={InvitesPage} />
					<Route exact path="/new" component={InvitesCreatePage} />
					<Route exact path="/edit/:id" component={InvitesEditPage} />
					<Route component={DefaultView} />
				</Switch>
			</HashRouter>
		);
		ReactDOM.render(routing, hubloy_membershipContainer);
	}
});