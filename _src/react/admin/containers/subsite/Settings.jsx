import React, { Component } from 'react';
import PropTypes from 'prop-types';
import LazyLoad from 'react-lazyload';

import Dashboard from 'layout/Dashboard'
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
			<Dashboard hubloy_membership={this.props.hubloy_membership}>
				<div className="hubloy_membership-settings uk-width-expand">
					<LazyLoad>
						<Nav hubloy_membership={this.props.hubloy_membership} active_nav={section}/>
					</LazyLoad>
					<div className="hubloy_membership-settings-settings uk-background-default uk-padding-small">
						{
							{
								'gateways': <SubSiteGateways hubloy_membership={this.props.hubloy_membership} />,
								'general': <SubSiteSettings hubloy_membership={this.props.hubloy_membership}/>
							}[section]
						}
					</div>
				</div>
			</Dashboard>
		)
	}
}

SiteSettings.propTypes = {
	hubloy_membership: PropTypes.object
};