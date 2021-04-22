import React, { Component } from 'react';
import PropTypes from 'prop-types';
import LazyLoad from 'react-lazyload';

import Dashboard from '../layout/Dashboard'
import SubSiteGateways from './sections/SubSiteGateways'

import Nav from './Nav'

export default class GatewaySettings extends Component {
	constructor(props) {
		super(props);
	}

	render() {
		return (
			<Dashboard hammock={this.props.hammock}>
				<div uk-grid="">
					<LazyLoad>
						<Nav hammock={this.props.hammock} active_nav={'gateways'}/>
					</LazyLoad>
					
					<div className="hammock-settings uk-width-expand uk-margin-left uk-card uk-card-body">
						<div className="hammock-settings-gateway">
							<SubSiteGateways hammock={this.props.hammock}/>
						</div>
					</div>
				</div>
			</Dashboard>
		)
	}
}

GatewaySettings.propTypes = {
	hammock: PropTypes.object
};