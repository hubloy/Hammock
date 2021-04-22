import React, { Component } from 'react';
import PropTypes from 'prop-types';

import Dashboard from '../layout/Dashboard'
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
				<h2 className="uk-text-center uk-heading-divider">{this.props.hammock.common.string.title}</h2>
				<SubSiteCodes type={`invitation`} hammock={this.props.hammock}/>
			</Dashboard>
		)
	}
}

InviteAddonSetting.propTypes = {
	hammock: PropTypes.object
};