import React, { Component } from 'react';
import PropTypes from 'prop-types';
import { Link } from 'react-router-dom';
import fetchWP from '../../../../utils/fetchWP';
import {PaginationUI, ToggleInfoBox} from '../../../ui/admin/form';


export default class Table extends Component {

    constructor(props) {
        super(props);
		this.state = {
            pager: {},
            items: [],
			loading : true,
			error : false,
        };

        this.fetchWP = new fetchWP({
			api_url: this.props.hammock.api_url,
			api_nonce: this.props.hammock.api_nonce,
        });
    }
    
    async componentDidMount() {
        this.loadPage();
    }

    async componentDidUpdate() {
		if ( typeof this.state.pager.current !== 'undefined' ) {
			this.loadPage();
		}
    }

    loadPage = async () => {
        const page = parseInt( location.hash.split("/").pop() ) || 1;
        if ( page !== this.state.pager.current ) {
            this.fetchWP.get( 'memberships/list?page=' + page )
                .then( (json) => this.setState({
                    items : json.items,
                    pager : json.pager,
					loading : false,
					error : false,
                }), (err) => this.setState({ loading : false, error : true })
            );
        }
    }


    render() {
        const { pager, items } = this.state;
        var hammock = this.props.hammock;
        var strings = hammock.strings;
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
					<React.Fragment>
						{items.length <= 0 ? (
							<ToggleInfoBox title={strings.dashboard.table.not_found} icon={'album'} linkText={strings.dashboard.add_new.button} linkTo={'#hammock-add-membership'}/>
						) : (
							<table className="uk-table">
								<thead>
									<tr>
										<th><input className="uk-checkbox hammock-top-checkbox" type="checkbox" /></th>
										<th>{strings.dashboard.table.name}</th>
										<th>{strings.dashboard.table.active}</th>
										<th>{strings.dashboard.table.type_description}</th>
										<th>{strings.dashboard.table.members}</th>
										<th>{strings.dashboard.table.price}</th>
										<th>{strings.dashboard.table.shortcode}</th>
									</tr>
								</thead>
								<tfoot>
									<tr>
										<th><input className="uk-checkbox hammock-bottom-checkbox" type="checkbox" /></th>
										<th>{strings.dashboard.table.name}</th>
										<th>{strings.dashboard.table.active}</th>
										<th>{strings.dashboard.table.type_description}</th>
										<th>{strings.dashboard.table.members}</th>
										<th>{strings.dashboard.table.price}</th>
										<th>{strings.dashboard.table.shortcode}</th>
									</tr>
								</tfoot>
								<tbody>
									{items.map(item =>
										<tr key={item.id}>
											<td><input className="uk-checkbox" type="checkbox" value={item.id} /></td>
											<td>
												{item.name}
												<div id={"membership-hover-"+ item.id}>
													<Link className="uk-text-primary" to={"/edit/" + item.id}>{hammock.common.buttons.edit}</Link>{' '}|{' '}<a href="#" className="uk-text-danger">{hammock.common.buttons.delete}</a>
												</div>
											</td>
											<td>{item.enabled}</td>
											<td>{item.type}</td>
											<td>{item.members}</td>
											<td><span dangerouslySetInnerHTML={{ __html: item.price }}></span></td>
											<td>
												<div className="uk-inline">
													<button className="uk-button uk-button-default uk-button-small" type="button">{strings.dashboard.table.shortcode}</button>
													<div uk-dropdown="pos: bottom-justify">
														<ul className="uk-nav uk-dropdown-nav">
															<li className="uk-nav-header">Header</li>
															<li className="uk-nav-header">Header</li>
															<li className="uk-nav-header">Header</li>
														</ul>
													</div>
												</div>
											</td>
										</tr>
									)}
								</tbody>
							</table>
						)}
						<PaginationUI pager={pager}/>
					</React.Fragment>
				)
			}
        }
    }
}