import React, { Component } from 'react';
import PropTypes from 'prop-types';
import { Link } from 'react-router-dom';
import Dashboard from 'layout/Dashboard'
import Nav from './rules/Nav';
import Content from './rules/Content';
import LazyLoad from 'react-lazyload';

export default class Rules extends Component {
	constructor(props) {
		super(props);
	}

	render() {
		var hammock = this.props.hammock,
		    active_nav = this.props.match.params.section !== undefined ? this.props.match.params.section : 'all',
			page = this.props.match.params.page !== undefined ? this.props.match.params.page : 0;
			
		return (
			<Dashboard hammock={hammock}>
				<div className="hammock-settings uk-width-expand">
					<LazyLoad>
						<Nav hammock={hammock} active_nav={active_nav}/>
					</LazyLoad>
					<div className="hammock-protection-rules uk-background-default uk-padding-small">
						<Content hammock={hammock} type={active_nav} page={page}/>
					</div>
				</div>
			</Dashboard>
		)
	}
}

Rules.propTypes = {
	hammock: PropTypes.object
};