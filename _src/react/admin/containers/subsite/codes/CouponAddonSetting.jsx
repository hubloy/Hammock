import React, { Component } from 'react';
import PropTypes from 'prop-types';

import Dashboard from 'layout/Dashboard'
import SubSiteCodes from './SubSiteCodes'

export default class CouponAddonSetting extends Component {

	constructor(props) {
		super(props);
	}


	handleUpdateAddonSetting( event ) {
		event.preventDefault();
	}


	render() {
		return (
			<Dashboard hammock={this.props.hammock}>
				<SubSiteCodes type={`coupons`} hammock={this.props.hammock}/>
			</Dashboard>
		)
	}
}

CouponAddonSetting.propTypes = {
	hammock: PropTypes.object
};