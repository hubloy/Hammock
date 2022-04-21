import React from 'react';
import ReactDOM from 'react-dom';
import { HashRouter, Route, Switch } from 'react-router-dom';

import NotFound from './admin/containers/NotFound';
import CouponAddonSetting from './admin/containers/subsite/codes/CouponAddonSetting'
import CouponAddonCreate from './admin/containers/subsite/codes/forms/CreateCode'
import CouponAddonEdit from './admin/containers/subsite/codes/forms/EditCode'

document.addEventListener('DOMContentLoaded', function () {
	const hubloy_membershipContainer = document.getElementById( 'hubloy_membership-coupons-container' );
	if ( hubloy_membershipContainer !== null ) {
		const DefaultView = (props) => <NotFound hubloy_membership={window.hubloy_membership} {...props}/>
		const CouponsPage = (props) => <CouponAddonSetting hubloy_membership={window.hubloy_membership} {...props} />
		const CouponsCreatePage = (props) => <CouponAddonCreate type={`coupons`} hubloy_membership={window.hubloy_membership} {...props} />
		const CouponsEditPage = (props) => <CouponAddonEdit type={`coupons`} hubloy_membership={window.hubloy_membership} {...props} />
		const routing = (
			<HashRouter>
				<Switch>
					<Route exact path="/" component={CouponsPage} />
					<Route exact path="/new" component={CouponsCreatePage} />
					<Route exact path="/edit/:id" component={CouponsEditPage} />
					<Route component={DefaultView} />
				</Switch>
			</HashRouter>
		);
		ReactDOM.render(routing, hubloy_membershipContainer);
	}
});