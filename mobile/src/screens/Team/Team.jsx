import { View } from "react-native";
import React, { useState } from "react";
import { Button } from "react-native-paper";
import { Layout, TeamList, TeamNotAssigned } from "../../components";

export const Team = () => {
  const STATUSES = {
    NOT_ASSIGNED: 'not_assigned',
    ASSIGNED: 'assigned',
    ACCEPTED: 'accepted'
  }
  const [status, setStatus] = useState(STATUSES.NOT_ASSIGNED);
  switch (status) {
    case STATUSES.ASSIGNED:
      return <Layout>
        <TeamList />
        <View style={{ marginTop: 'auto',  paddingTop: 16}}>
          <Button mode="outlined" raised onPress={() => setStatus(STATUSES.NOT_ASSIGNED)}>Бригада не готова к дежурству</Button>
          <Button mode="contained" style={{marginTop: 16}} onPress={() => setStatus(STATUSES.ACCEPTED)}>Бригада вышла на дежурство</Button>
        </View>
      </Layout>
    case STATUSES.ACCEPTED:
      return <Layout>
        <TeamList />
        <View style={{ marginTop: 'auto',  paddingTop: 16}}>
          <Button mode="outlined" onPress={() => setStatus(STATUSES.NOT_ASSIGNED)}>Завершить смену</Button>
        </View>
      </Layout>
    default:
      return <TeamNotAssigned onPress={() => setStatus(STATUSES.ASSIGNED)} />
  }
}
