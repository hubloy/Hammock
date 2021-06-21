import React from 'react';
import ReactDOM from 'react-dom';
import { HashRouter, Route, Switch } from 'react-router-dom';

import NotFound from './admin/containers/NotFound';
import CouponAddonSetting from './admin/containers/subsite/codes/CouponAddonSetting'
import CouponAddonCreate from './admin/containers/subsite/codes/forms/CreateCode'
import CouponAddonEdit from './admin/containers/subsite/codes/forms/EditCode'

document.addEventListener('DOMContentLoaded', function () {
	const hammockContainer = document.getElementById( 'hammock-coupons-container' );
	if ( hammockContainer !== null ) {
		const DefaultView = (props) => <NotFound hammock={window.hammock} {...props}/>
		const CouponsPage = (props) => <CouponAddonSetting hammock={window.hammock} {...props} />
		const CouponsCreatePage = (props) => <CouponAddonCreate type={`coupons`} hammock={window.hammock} {...props} />
		const CouponsEditPage = (props) => <CouponAddonEdit type={`coupons`} hammock={window.hammock} {...props} />
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
		ReactDOM.render(routing, hammockContainer);
	}
});