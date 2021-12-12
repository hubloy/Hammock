import React, { Component } from 'react';
import PropTypes from 'prop-types';

import Dashboard from 'layout/Dashboard'
import fetchWP from 'utils/fetchWP';
import { Nav } from './comms/Nav'
import Table from './comms/Table'

export default class Comms extends Component {
	constructor(props) {
		super(props);

		this.state = {
			items : [],
			loading : true,
			error : false,
		};
		
		this.fetchWP = new fetchWP({
			api_url: this.props.hammock.api_url,
			api_nonce: this.props.hammock.api_nonce,
        });
	}

	async componentDidMount() {
		this.fetchWP.get( 'emails/list' )
			.then( (json) => this.setState({
				items : json,
				loading : false,
				error : false,
			}), (err) => this.setState({ loading : false, error : true })
		);
	}

	render() {
		const items = this.state.items;
		var hammock = this.props.hammock;
		if ( this.state.loading) {
            return (
                <div className="uk-container uk-padding-small uk-margin-top uk-width-1-1 uk-background-default">
                    <span className="uk-text-center" uk-spinner="ratio: 3"></span>
                </div>
            )
        } else {
			if ( this.state.error) {
				return (
					<h3 className="uk-text-center uk-text-danger">{hammock.error}</h3>
				)
			} else {
				return (
					<Dashboard hammock={hammock}>
						{typeof items.admin !== 'undefined' &&
							<React.Fragment>
								<Nav hammock={hammock}/>
								<div className="hammock-comms uk-container uk-padding-small uk-margin-top uk-width-1-1 uk-background-default uk-switcher">
									<div>
										<Table hammock={hammock} type={`admin`} items={items.admin}/>
									</div>
									<div>
										<Table hammock={hammock} type={`member`} items={items.member}/>
									</div>
								</div>
							</React.Fragment>
						}
					</Dashboard>
				)
			}
		}
	}
}

Comms.propTypes = {
	hammock: PropTypes.object
};