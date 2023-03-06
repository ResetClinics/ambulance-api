import React from 'react'
import {
  RefreshControl, ScrollView,
} from 'react-native'
import { COLORS } from '../../../constants'
import { Layout } from '../Layout'

export const TeamLayout = ({ children, refreshing, onRefresh }) => (
  <Layout>
    <ScrollView
      showsVerticalScrollIndicator={false}
      refreshControl={(
        <RefreshControl
          refreshing={refreshing}
          onRefresh={onRefresh}
          tintColor={COLORS.primary}
          colors={['#04607A']}
        />
      )}
    >
      {children}
    </ScrollView>
  </Layout>
)
