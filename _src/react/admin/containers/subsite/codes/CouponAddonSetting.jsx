import React, { Component } from 'react';
import PropTypes from 'prop-types';
import { Link } from 'react-router-dom';
import Dashboard from 'layout/Dashboard'
import SubSiteCodes from './SubSiteCodes'

export default class CouponAddonSetting extends Component {

	constructor(props) {
		super(props);
	}

	render() {
		var hubloy_membership = this.props.hubloy_membership;
		return (
			<Dashboard hubloy_membership={hubloy_membership} button={<Link className="uk-button uk-button-primary uk-button-small" to={"/new"}>{hubloy_membership.strings.add.coupon}</Link>}>
				<SubSiteCodes type={`coupons`} hubloy_membership={hubloy_membership}/>
			</Dashboard>
		)
	}
}

CouponAddonSetting.propTypes = {
	hubloy_membership: PropTypes.object
};