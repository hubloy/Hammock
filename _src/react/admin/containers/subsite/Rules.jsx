import React, { Component } from 'react';
import PropTypes from 'prop-types';
import { Link } from 'react-router-dom';
import Dashboard from 'layout/Dashboard'
import Nav from './rules/Nav';
import Content from './rules/Content';
import AddContent from './rules/Add';
import LazyLoad from 'react-lazyload';

import fetchWP from 'utils/fetchWP'

export default class Rules extends Component {
	constructor(props) {
		super(props);
		this.state = {
			rules : [],
			loading : true
		};
		this.fetchWP = new fetchWP({
			api_url: this.props.hubloy_membership.api_url,
			api_nonce: this.props.hubloy_membership.api_nonce,
		});
	}

	async componentDidMount() {
		this.load_nav()
	}

	load_nav = async () => {
		this.fetchWP.get( 'rules/list' )
			.then( (json) => this.setState({
				rules : json,
				loading : false
			}), (err) => this.setState({ loading : false })
		);
	}

	render() {
		var hubloy_membership = this.props.hubloy_membership,
			strings = hubloy_membership.strings,
		    active_nav = this.props.match.params.section !== undefined ? this.props.match.params.section : 'all',
			page = this.props.match.params.page !== undefined ? this.props.match.params.page : 0;
			
		return (
			<Dashboard hubloy_membership={hubloy_membership} button={<a className="uk-button uk-button-primary uk-button-small" href="#hubloy_membership-add-rule" uk-toggle="">{strings.dashboard.add_new.button}</a>}>
				<div className="hubloy_membership-settings uk-width-expand">
					{this.state.loading ? (
                        <span className="uk-text-center" uk-spinner="ratio: 2"></span>
                    ) : (
						<React.Fragment>
							<LazyLoad>
								<Nav hubloy_membership={hubloy_membership} active_nav={active_nav} rules={this.state.rules}/>
							</LazyLoad>
							<div className="hubloy_membership-protection-rules uk-background-default uk-padding-small">
								<Content hubloy_membership={hubloy_membership} type={active_nav} page={page} rules={this.state.rules}/>
							</div>
							<AddContent hubloy_membership={hubloy_membership} active_nav={active_nav} rules={this.state.rules} />
						</React.Fragment>
					)}
				</div>
			</Dashboard>
		)
	}
}

Rules.propTypes = {
	hubloy_membership: PropTypes.object
};