import React, { Component } from 'react';
import PropTypes from 'prop-types';

import Dashboard from 'layout/Dashboard'
import SubSiteCodes from './SubSiteCodes'

export default class InviteAddonSetting extends Component {

	constructor(props) {
		super(props);
	}


	handleUpdateAddonSetting( event ) {
		event.preventDefault();
	}


	render() {
		return (
			<Dashboard hammock={this.props.hammock}>
				<SubSiteCodes type={`invitation`} hammock={this.props.hammock}/>
			</Dashboard>
		)
	}
}

InviteAddonSetting.propTypes = {
	hammock: PropTypes.object
};