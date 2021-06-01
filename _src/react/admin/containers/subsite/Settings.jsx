import React, { Component } from 'react';
import PropTypes from 'prop-types';
import LazyLoad from 'react-lazyload';

import Dashboard from './layout/Dashboard'
import SubSiteGateways from './settings/sections/SubSiteGateways'
import SubSiteSettings from './settings/sections/SubSiteSettings'

import Nav from './settings/Nav'

export default class SiteSettings extends Component {
	constructor(props) {
		super(props);
	}

	render() {
		var section = this.props.match.params.section !== undefined ? this.props.match.params.section : 'general';
		return (
			<Dashboard hammock={this.props.hammock}>
				<div uk-grid="">
					<LazyLoad className="uk-width-1-4 uk-height-medium">
						<Nav hammock={this.props.hammock} active_nav={section}/>
					</LazyLoad>
					
					<div className="hammock-settings uk-width-expand uk-margin-left uk-card uk-card-body">
						<div className="hammock-settings-settings uk-background-default uk-padding-small uk-border-rounded">
							{
								{
									'gateways': <SubSiteGateways hammock={this.props.hammock} />,
									'general': <SubSiteSettings hammock={this.props.hammock}/>
								}[section]
							}
						</div>
					</div>
				</div>
			</Dashboard>
		)
	}
}

SiteSettings.propTypes = {
	hammock: PropTypes.object
};