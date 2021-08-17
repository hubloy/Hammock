import React, { Component } from 'react';

import fetchWP from 'utils/fetchWP';

import {Preloader, Center} from 'ui/admin/form';
import { Bar, Line } from 'react-chartjs-2';

import { toast } from 'react-toastify';

export default class StatsDashboard extends Component {

	constructor(props) {
		super(props);
		this.state = {
			loading_subs : true,
			loading_trans : true,
			subscribers : [],
			transactions : []
        };
        this.fetchWP = new fetchWP({
			api_url: this.props.hammock.api_url,
			api_nonce: this.props.hammock.api_nonce,
        });
	}

	notify(type, message) {
		toast[type](message, {toastId: 'dashboard-stats-toast'});
	}

	componentDidMount() {
		Promise.all([this.load_subscribers(), this.load_transactions()]);
	}

	load_subscribers = async() => {
		this.fetchWP.get( 'dashboard/subscribers' )
			.then( (json) => this.setState({
				subscribers : json,
				loading_subs : false,
				error : false,
			}), (err) => {
				this.setState({ loading_subs : false, error : true });
				this.notify( this.props.hammock.error, 'error' );
			}
		);
	}

	load_transactions = async() => {
		this.fetchWP.get( 'dashboard/transactions' )
			.then( (json) => this.setState({
				transactions : json,
				loading_trans : false,
				error : false,
			}), (err) => {
				this.setState({ loading_trans : false, error : true });
				this.notify( this.props.hammock.error, 'error' );
			}
		);
	}


	render() {
		var subscribers = this.state.subscribers;
		var transactions = this.state.transactions;
		var hammock = this.props.hammock,
			days = hammock.strings.dashboard.stats.charts.days;
		const options = {
			scales: {
				yAxes: [
					{
						ticks: {
							beginAtZero: true,
						},
					},
				],
			},
			plugins: {
				legend: {
					onClick: function(e, item) {
						return;
					}
				}
			}
		};
		return (
			<React.Fragment>
					
				<div className="uk-background-default uk-padding-small uk-margin-medium-top uk-panel uk-height-medium">
					{this.state.loading_subs ? (
						<Preloader />
					) : (
						subscribers.length > 0 ? (
							<Bar data={{
								labels: [days.mon, days.tue, days.wed, days.thu, days.fri, days.sat, days.sun],
								datasets: [
									{
										label: hammock.strings.dashboard.stats.charts.subscribers,
										data: [subscribers.mon, subscribers.tue, subscribers.wed, subscribers.thu, subscribers.fri, subscribers.sat, subscribers.sun],
										backgroundColor: 'rgb(49, 104, 142)',
										borderColor: 'rgb(49, 104, 142)',
										borderWidth: 1,
									},
								],
							}} options={options}/>
						) : (
							<Center text={hammock.strings.dashboard.stats.no_data.subscribers} backgroundImage={hammock.assets_url + '/img/preloader-chart.jpg'}/>
						)
					)}
				</div>
				<div className="uk-background-default uk-padding-small uk-margin-medium-top uk-panel uk-height-medium">
					{this.state.loading_trans ? (
						<Preloader />
					) : (
						transactions.length > 0 ? (
							<Line data={{
								labels: [days.mon, days.tue, days.wed, days.thu, days.fri, days.sat, days.sun],
								datasets: [
									{
										label: hammock.strings.dashboard.stats.charts.transactions,
										data: [transactions.mon, transactions.tue, transactions.wed, transactions.thu, transactions.fri, transactions.sat, transactions.sun],
										fill: false,
										backgroundColor: 'rgb(49, 104, 142)',
										borderColor: 'rgba(49, 104, 142)',
									},
								],
							}} options={options} />
						) : (
							<Center text={hammock.strings.dashboard.stats.no_data.transactions} backgroundImage={hammock.assets_url + '/img/preloader-chart.jpg'}/>
						)
					)}
				</div>
			</React.Fragment>
		)
	}
}