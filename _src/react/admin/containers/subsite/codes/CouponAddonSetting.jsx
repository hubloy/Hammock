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
		var hammock = this.props.hammock;
		return (
			<Dashboard hammock={hammock} button={<Link className="uk-button uk-button-primary uk-button-small" to={"/new"}>{hammock.strings.add.coupon}</Link>}>
				<SubSiteCodes type={`coupons`} hammock={hammock}/>
			</Dashboard>
		)
	}
}

CouponAddonSetting.propTypes = {
	hammock: PropTypes.object
};