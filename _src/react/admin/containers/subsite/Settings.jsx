import React, { Component } from 'react';
import PropTypes from 'prop-types';
import LazyLoad from 'react-lazyload';

import Dashboard from './layout/Dashboard'
import SubSiteSettings from './settings/sections/SubSiteSettings'

import Nav from './settings/Nav'

export default class SiteSettings extends Component {
	constructor(props) {
		super(props);
	}

	render() {
		return (
			<Dashboard hammock={this.props.hammock}>
				<div uk-grid="">
					<LazyLoad>
						<Nav hammock={this.props.hammock} active_nav={'general'}/>
					</LazyLoad>
					
					<div className="hammock-settings uk-width-expand uk-margin-left uk-card uk-card-body">
						<div className="hammock-settings-settings uk-background-default uk-padding-small uk-border-rounded">
							<SubSiteSettings hammock={this.props.hammock}/>
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