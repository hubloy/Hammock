import React, { Component } from 'react';
import PropTypes from 'prop-types';

import fetchWP from 'utils/fetchWP';
import GatewaySetting from './gateways/GatewaySetting';


export default class SubSiteGateways extends Component {

    constructor(props) {
        super(props);
        
        this.fetchWP = new fetchWP({
			api_url: this.props.hubloy_membership.api_url,
			api_nonce: this.props.hubloy_membership.api_nonce,
        });
        
        this.state = {
            items: [],
            loading : true,
			error : false
        };
    }
    
    async componentDidMount() {
        this.loadPage();
	}
	
	loadPage = async () => {
		this.fetchWP.get( 'gateways/list' )
			.then( (json) => this.setState({
                items : json,
                loading : false,
				error : false
			}), (err) => this.setState({ loading : false, error : true })
		);
    }
    
    render() {
        if ( this.state.loading ) {
			return (
				<div className="uk-container uk-padding-small uk-margin-top uk-width-1-1 uk-background-default">
					<span className="uk-text-center" uk-spinner="ratio: 3"></span>
				</div>
			)
		} else {
            if ( this.state.error) {
				return (
					<h3 className="uk-text-center uk-text-danger">{this.props.hubloy_membership.error}</h3>
				)
			} else {
                const { items } = this.state;
                var hubloy_membership = this.props.hubloy_membership;
                return (
                    <div className="uk-padding-small">
                        <ul uk-accordion="">
                            {Object.keys(items).map(item =>
                                <React.Fragment key={item}>
                                    <GatewaySetting hubloy_membership={hubloy_membership} id={item} item={items[item]} key={item} />
                                </React.Fragment>
                            )}
                        </ul>
                    </div>
                )
            }
        }
        
    }
}
SubSiteGateways.propTypes = {
	hubloy_membership: PropTypes.object
};