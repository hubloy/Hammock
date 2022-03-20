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
			api_url: this.props.hammock.api_url,
			api_nonce: this.props.hammock.api_nonce,
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
		var hammock = this.props.hammock,
			strings = hammock.strings,
		    active_nav = this.props.match.params.section !== undefined ? this.props.match.params.section : 'all',
			page = this.props.match.params.page !== undefined ? this.props.match.params.page : 0;
			
		return (
			<Dashboard hammock={hammock} button={<a className="uk-button uk-button-primary uk-button-small" href="#hammock-add-rule" uk-toggle="">{strings.dashboard.add_new.button}</a>}>
				<div className="hammock-settings uk-width-expand">
					{this.state.loading ? (
                        <span className="uk-text-center" uk-spinner="ratio: 2"></span>
                    ) : (
						<React.Fragment>
							<LazyLoad>
								<Nav hammock={hammock} active_nav={active_nav} rules={this.state.rules}/>
							</LazyLoad>
							<div className="hammock-protection-rules uk-background-default uk-padding-small">
								<Content hammock={hammock} type={active_nav} page={page}/>
							</div>
							<AddContent hammock={hammock} active_nav={active_nav} rules={this.state.rules} />
						</React.Fragment>
					)}
				</div>
			</Dashboard>
		)
	}
}

Rules.propTypes = {
	hammock: PropTypes.object
};